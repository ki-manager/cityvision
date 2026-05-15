import streamlit as st
from utils.components import card

def show():
    st.title("Startseite")

    card(
        "CityVision",
        "Darstellung und Auswertung von Smart-City- und Umweltdaten"
    )
