import streamlit as st
import matplotlib.pyplot as plt

from modules.api.luen_api import LuenAPI
from modules.classification.environment_classifier import EnvironmentClassifier

st.title("Messpunkte")

api = LuenAPI()

data = api.download_data()

df = api.create_dataframe(data)

stations = df["Station"].unique()

selected_station = st.selectbox(
    "Messpunkt auswählen",
    stations
)

station_df = df[df["Station"] == selected_station]

avg_df = (
    station_df.groupby("Komponente")["Messwert"]
    .mean()
    .reset_index()
)

classifier = EnvironmentClassifier()

colors = []

for value in avg_df["Messwert"]:

    status = classifier.classify_temperature(value)

    if status == "Kalt":
        colors.append("blue")
    elif status == "Warm":
        colors.append("green")
    elif status == "Heiß":
        colors.append("orange")
    else:
        colors.append("red")

fig, ax = plt.subplots(figsize=(12, 5))

ax.bar(
    avg_df["Komponente"],
    avg_df["Messwert"],
    color=colors
)

plt.xticks(rotation=45)

st.pyplot(fig)
