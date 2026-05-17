import streamlit as st
from utils.components import card
from PIL import Image

def show():
    logo = Image.open("assets/images/logo.png")
    st.image(logo, width=600)
