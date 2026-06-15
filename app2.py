import streamlit as st

st.set_page_config(page_title="CityVision", layout="wide")

st.sidebar.title("CityVision")

menu = st.sidebar.selectbox(
    "Navigation",
    ["Startseite", "Datenanalyse", "Messpunkte", "Vorhersagen"]
)

if menu == "Startseite":
    import seiten.start
elif menu == "Datenanalyse":
    import seiten.data_analysis
elif menu == "Messpunkte":
    import seiten.measurement_points
elif menu == "Vorhersagen":
    import seiten.predictions
