import streamlit as st

def card(title: str, body: str):
    st.markdown(
        f"""
        <div class='card'>
            <h4>{title}</h4>
            {body}
        </div>
        """,
        unsafe_allow_html=True,
    )

def stat(num: str, label: str):
    st.markdown(
        f"""
        <div class='stat'>
            <div class='num'>{num}</div>
            <div class='lbl'>{label}</div>
        </div>
        """,
        unsafe_allow_html=True,
    )
