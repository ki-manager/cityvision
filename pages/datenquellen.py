import streamlit as st
from utils.components import card

def show():
    st.title("Datenquellen")

    quellen = [
        ("🌦️ Wetterdaten", "Temperatur und Wind"),
        ("🚦 Verkehr", "Staus und Verkehrsfluss"),
        ("🌫️ Luftqualität", "Feinstaub und NO₂"),
    ]

    cols = st.columns(3)

    for i, (t, b) in enumerate(quellen):
        with cols[i % 3]:
            card(t, b)
