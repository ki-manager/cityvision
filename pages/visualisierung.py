import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def show():
    st.title("Visualisierung")

    days = pd.date_range("2025-05-01", periods=30)

    df = pd.DataFrame({
        "Datum": days,
        "Temperatur": 15 + 8*np.sin(np.linspace(0, 3, 30)),
    })

    fig, ax = plt.subplots(figsize=(10, 4))

    sns.lineplot(
        data=df,
        x="Datum",
        y="Temperatur",
        ax=ax
    )

    st.pyplot(fig)
