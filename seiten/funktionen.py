import streamlit as st
from utils.components import card

def show():
    st.title("Funktionen")

    funktionen = [
        ("📡 Datenerfassung", "Erfassung und Verarbeitung unterschiedlicher Smart-City-Daten"),
        ("🌦️ Datenanalyse", "Analyse von Wetter-, Umwelt- und Verkehrsdaten"),
        ("📊 Visualisierung", "Darstellung der Ergebnisse in Diagrammen und Dashboards"),
        ("🖥️ Benutzeroberfläche", "Interaktive Oberfläche mit Streamlit"),
        ("🔗 Datenintegration", "Vergleich und Kombination mehrerer Datenquellen"),
        ("📈 Trendanalysen", "Visualisierung von Trends und Entwicklungen"),
        ("🤖 KI & ML", "Grundlage für spätere KI- und Machine-Learning-Anwendungen"),
    ]

    cols = st.columns(3)

    for i, (t, b) in enumerate(funktionen):
        with cols[i % 3]:
            card(t, b)