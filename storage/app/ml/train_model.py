import json
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
import joblib

# загрузка данных
with open('assets.json') as f:
    data = json.load(f)

df = pd.DataFrame(data)

X = df[['usage_years', 'scan_count']]
y = df['write_off']  # 1 = списан, 0 = работает

model = RandomForestClassifier(n_estimators=100)
model.fit(X, y)

joblib.dump(model, 'asset_risk_model.pkl')
print("Model trained")
