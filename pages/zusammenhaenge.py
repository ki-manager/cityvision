import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def show():
    st.title("Zusammenhänge")

    df = pd.DataFrame({
        "Temperatur": np.random.normal(20, 5, 100),
        "Energie": np.random.normal(80, 10, 100),
    })

    fig, ax = plt.subplots(figsize=(6, 5))

    sns.heatmap(df.corr(), annot=True, cmap="crest", ax=ax)

    st.pyplot(fig)
