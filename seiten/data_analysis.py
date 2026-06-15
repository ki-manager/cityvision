import streamlit as st

from modules.api.luen_api import LuenAPI
from modules.preprocessing.cleaner import DataCleaner

st.title("Datenanalyse")

api = LuenAPI()

data = api.download_data()

df = api.create_dataframe(data)

cleaner = DataCleaner()

df = cleaner.clean_dataframe(df)

st.metric("Datensätze", len(df))

st.dataframe(df.head())

st.subheader("Statistik")

st.dataframe(df.describe())
