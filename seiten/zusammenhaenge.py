import streamlit as st
from utils.components import card
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def show():
    st.title("Zusammenhänge")

    c1, c2, c3, c4 = st.columns(4)

    with c1:
        card("🔗 Korrelationen", "Statistische Beziehungen zwischen Datenströmen.")

    with c2:
        card("📈 Trends", "Längerfristige Entwicklungen sichtbar machen.")

    with c3:
        card("🧩 Muster", "Wiederkehrende Verläufe in der Stadt erkennen.")

    with c4:
        card("⚠️ Auffälligkeiten", "Anomalien und Ausreißer hervorheben.")

    np.random.seed(0)

    n = 200

    temp = np.random.normal(20, 5, n)
    energy = 100 - 1.8 * temp + np.random.normal(0, 8, n)
    traffic = 800 + 5 * temp + np.random.normal(0, 60, n)
    pm = 30 - 0.4 * temp + np.random.normal(0, 4, n)

    df = pd.DataFrame({
        "Temperatur": temp,
        "Energie": energy,
        "Verkehr": traffic,
        "Feinstaub": pm
    })

    st.subheader("Korrelationsmatrix")

    fig, ax = plt.subplots(figsize=(8, 6))

    sns.heatmap(
        df.corr(),
        annot=True,
        cmap="crest",
        linewidths=0.5,
        ax=ax
    )

    ax.set_title("Korrelationsmatrix")

    st.pyplot(fig)