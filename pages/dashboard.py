import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

from utils.components import stat

def show():
    st.title("Live-Dashboard")

    tage = st.slider("Zeitraum", 7, 90, 30)

    days = pd.date_range(end=pd.Timestamp.today(), periods=tage)

    df = pd.DataFrame({
        "Datum": days,
        "Temperatur": 15 + np.random.randn(tage),
    })

    stat(f"{df['Temperatur'].mean():.1f}°C", "Ø Temperatur")

    fig, ax = plt.subplots(figsize=(10, 4))

    sns.lineplot(data=df, x="Datum", y="Temperatur", ax=ax)

    st.pyplot(fig)
