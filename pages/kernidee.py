import streamlit as st
from utils.components import card

def show():
    st.title("Kernidee")

    card(
        "Projektbeschreibung",
        "Analyse von Wetter-, Verkehrs- und Umweltdaten."
    )
