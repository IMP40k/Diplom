FROM python:3.11-slim

WORKDIR /app

# зависимости
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# код
COPY . .

# обучение модели
RUN python train_model.py

# запуск waf
CMD ["python", "waf.py"]

