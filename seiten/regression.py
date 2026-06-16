import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from sklearn.linear_model import (
    LinearRegression
)


st.title("Regression")

FILE_PATH = "data/stations/BGVT.csv"


df = pd.read_csv(FILE_PATH)

df = df[
    df["Komponente"] == "TT"
]


df["Messwert"] = pd.to_numeric(
    df["Messwert"],
    errors="coerce"
)

df = df.dropna()


X = df[["Zeitindex"]]

y = df["Messwert"]


model = LinearRegression()

model.fit(X, y)


predictions = model.predict(X)


fig, ax = plt.subplots(
    figsize=(12, 5)
)

ax.plot(
    df["Zeitindex"],
    y,
    label="Original"
)

ax.plot(
    df["Zeitindex"],
    predictions,
    label="Regression"
)

ax.legend()

ax.grid(True)

st.pyplot(fig)