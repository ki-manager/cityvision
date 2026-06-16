import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report


# =====================================================
# Titel
# =====================================================

st.title("Klassifikation")

st.subheader(
    "Luftqualitätsklassifikation "
    "Braunschweig"
)


# =====================================================
# Datei laden
# =====================================================

FILE_PATH = "data/stations/BGVT.csv"


if not os.path.exists(FILE_PATH):

    st.error(
        "BGVT.csv nicht gefunden"
    )

    st.stop()


df = pd.read_csv(FILE_PATH)


# =====================================================
# PM10 filtern
# =====================================================

df = df[
    df["Komponente"] == "PM1024"
]


df["Messwert"] = pd.to_numeric(

    df["Messwert"],

    errors="coerce"

)


df = df.dropna()


# =====================================================
# Klassen erzeugen
# =====================================================

def classify(value):

    if value < 20:
        return 0

    elif value < 40:
        return 1

    else:
        return 2


df["Klasse"] = df[
    "Messwert"
].apply(classify)


# =====================================================
# Features
# =====================================================

X = df[["Zeitindex"]]

y = df["Klasse"]


# =====================================================
# Training
# =====================================================

X_train, X_test, y_train, y_test = (
    train_test_split(
        X,
        y,
        test_size=0.2
    )
)


model = RandomForestClassifier()

model.fit(
    X_train,
    y_train
)


predictions = model.predict(
    X_test
)


# =====================================================
# Bericht
# =====================================================

st.text(

    classification_report(
        y_test,
        predictions
    )

)


# =====================================================
# Diagramm
# =====================================================

fig, ax = plt.subplots(
    figsize=(12, 5)
)


ax.scatter(

    df["Zeitindex"],

    df["Messwert"],

    c=df["Klasse"]

)


ax.grid(True)

st.pyplot(fig)