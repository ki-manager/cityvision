import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt

from modules.api.luen_api import LuenAPI
from modules.prediction.predictor import Predictor

st.title("Vorhersagen")

api = LuenAPI()

data = api.download_data()

df = api.create_dataframe(data)

stations = df["Station"].unique()

selected_station = st.selectbox(
    "Standort auswählen",
    stations
)

station_df = df[df["Station"] == selected_station]

components = station_df["Komponente"].unique()

selected_component = st.selectbox(
    "Messwert auswählen",
    components
)

values = station_df[
    station_df["Komponente"] == selected_component
]["Messwert"].values

predictor = Predictor()

predictions = predictor.create_prediction(values)

future_df = pd.DataFrame({
    "Vorhersage": predictions
})

st.dataframe(future_df)

fig, ax = plt.subplots(figsize=(12, 5))

ax.plot(values, label="Messwerte")

future_x = range(
    len(values),
    len(values) + len(predictions)
)

ax.plot(
    future_x,
    predictions,
    label="Vorhersage"
)

ax.legend()

st.pyplot(fig)
