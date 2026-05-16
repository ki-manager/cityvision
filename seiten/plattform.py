import streamlit as st
from utils.components import card

def show():
    st.title("Analyseplattform")

    card(
        "Kern-Plattform",
        "CityVision bildet die Basis für Datenintegration, Visualisierung "
        "und Auswertung – modular aufgebaut und erweiterbar."
    )

    cols = st.columns(5)

    module = [
        ("🤖 Machine Learning", "Modelle für Klassifikation und Regression."),
        ("⚡ Echtzeitdaten", "Streaming-Quellen anbinden und auswerten."),
        ("💬 KI-Assistenten", "Natürlichsprachliche Datenexploration."),
        ("🔮 Prognosemodelle", "Vorhersagen für Wetter, Verkehr, Energie."),
        ("⚙️ Automatisierung", "Reports und Analysen automatisieren."),
    ]

    for col, (t, b) in zip(cols, module):
        with col:
            card(t, b)