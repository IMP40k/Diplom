import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.svm import LinearSVC
import joblib, os

df = pd.read_csv(
    "data/train.csv",
    quotechar='"',
    escapechar='\\',
    engine="python"
)


X = df["text"]
y = df["label"]

vectorizer = TfidfVectorizer(
    ngram_range=(1,2),
    max_features=5000,
    lowercase=True
)

X_vec = vectorizer.fit_transform(X)

clf = LinearSVC()
clf.fit(X_vec, y)

os.makedirs("model", exist_ok=True)
joblib.dump(clf, "model/clf.joblib")
joblib.dump(vectorizer, "model/vectorizer.joblib")

print("[+] ML model trained")

