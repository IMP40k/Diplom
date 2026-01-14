from flask import Flask, request, Response
import html
import urllib.parse
import re
import requests, joblib, logging
from urllib.parse import urljoin

BACKEND = "http://backend:80"

clf = joblib.load("model/clf.joblib")
vectorizer = joblib.load("model/vectorizer.joblib")

logging.basicConfig(
    filename="logs/waf.log",
    level=logging.INFO,
    format="%(asctime)s BLOCKED %(message)s"
)

app = Flask(__name__)

def normalize(text: str) -> str:
    text = urllib.parse.unquote_plus(text)
    text = html.unescape(text)
    text = text.lower()
    text = re.sub(r"\s+", " ", text)
    return text.strip()

def extract(req):
    parts = []

    # метод + путь
    parts.append(req.method)
    parts.append(req.path)

    # QUERY STRING (ГЛАВНОЕ)
    if req.query_string:
        parts.append(urllib.parse.unquote_plus(
            req.query_string.decode(errors="ignore")
        ))

    # BODY
    body = req.get_data(as_text=True)
    if body:
        parts.append(urllib.parse.unquote_plus(body))

    # ТОЛЬКО опасные headers
    for h in ["user-agent", "referer", "cookie"]:
        if h in req.headers:
            parts.append(req.headers[h])

    text = " ".join(parts)
    return normalize(text)

@app.route("/", defaults={"path": ""}, methods=["GET","POST","PUT","DELETE"])
@app.route("/<path:path>", methods=["GET","POST","PUT","DELETE"])
def proxy(path):
    text = extract(request)

    X = vectorizer.transform([text])
    if clf.predict(X)[0] == 1:
        logging.warning(f"{request.remote_addr} {request.full_path}")
        return Response("Blocked by ML-WAF", 403)

    resp = requests.request(
        method=request.method,
        url=urljoin(BACKEND + "/", path),
        headers={
            **{k: v for k, v in request.headers if k.lower() != "host"},
            "Accept-Encoding": "identity"
        },
        params=request.args,
        data=request.get_data(),
        cookies=request.cookies,
        allow_redirects=False
    )

    excluded_headers = [
        "content-encoding",
        "content-length",
        "transfer-encoding",
        "connection"
    ]

    headers = [
        (name, value)
        for (name, value) in resp.headers.items()
        if name.lower() not in excluded_headers
    ]

    return Response(resp.content, resp.status_code, headers)

if __name__ == "__main__":
    print("ML-WAF started on port 8080")
    app.run(host="0.0.0.0", port=8080)

