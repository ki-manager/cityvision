from sklearn.ensemble import RandomForestRegressor
import pandas as pd

class Predictor:

    def create_prediction(self, values):

        df = pd.DataFrame({
            "Zeit": range(len(values)),
            "Messwert": values
        })

        X = df[["Zeit"]]
        y = df["Messwert"]

        model = RandomForestRegressor()

        model.fit(X, y)

        future = pd.DataFrame({
            "Zeit": range(len(values), len(values) + 12)
        })

        predictions = model.predict(future)

        return predictions
