import streamlit as st
from utils.components import card
from PIL import Image


logo = Image.open("assets//images//logo.jpg")
st.image(logo, width=400)

st.title("Beschreibung des Projekts")

card(
    "Entstehung der Idee",
    "Die Idee zu CityVision entstand aus dem zunehmenden Interesse an Smart-City-Technologien, Datenanalyse und künstlicher Intelligenz. Moderne Städte erzeugen täglich große Mengen an Daten – beispielsweise durch Wetterstationen, Verkehrssysteme, Umweltmessungen oder digitale Infrastrukturen."
)

card(
    "Datenquellen ",
    "Die benötigtenDaten liegen häufig verteilt in unterschiedlichen Formaten vor und sind für viele Menschen nur schwer verständlich. Das Ziel von CityVision ist es daher, verschiedene Datenquellen zusammenzuführen, übersichtlich darzustellen und für Analysen nutzbar zu machen."
)

card(
    "Implementierung",
    "Für die Umsetzung kommen Python-Bibliotheken wie Pandas, NumPy, Matplotlib, Seaborn und Streamlit zum Einsatz."
)