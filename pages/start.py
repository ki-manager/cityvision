import streamlit as st
from utils.components import card
from PIL import Image

def show():
    st.title("Startseite")

    logo = Image.open("c:/Users/User/OneDrive/Dokumente/GitHub/educx-python/cityvision/logo.png")
    st.image(logo, width=600)

    card(
        "CityVision",
        "Darstellung und Auswertung von Smart-City- und Umweltdaten"
    )
