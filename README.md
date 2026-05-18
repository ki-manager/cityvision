# CityVision

CityVision ist eine interaktive Smart-City- und Umweltdatenplattform mit Python und Streamlit.

Die Anwendung visualisiert:
- Wetterdaten
- Verkehrsdaten
- Luftqualität
- Energieverbrauch
- Statistische Zusammenhänge


# Features

- Moderne Dashboards
- Interaktive Diagramme
- JSON-Datenintegration
- Heatmaps & Korrelationen
- Responsive Layouts
- Modulare Seitenstruktur
- Sidebar-Navigation


# Verwendete Technologien

- Python
- Streamlit
- Pandas
- NumPy
- Matplotlib
- Seaborn
- Pillow


# Projektstruktur

```text
cityvision/
│
├── app.py
├── data/json/
├── seiten/
├── utils/
└── assets/
```


# Anwendung starten

## Installation

```bash
pip install streamlit pandas numpy matplotlib seaborn pillow
```

## Start

```bash
streamlit run app.py
```

Die Anwendung startet anschließend unter:

```text
http://localhost:8501
```


# Beispiel: JSON-Daten laden

```python
import json

with open("data/json/wetter.json", "r") as f:
    data = json.load(f)
```


# Beispiel: Diagramm erzeugen

```python
fig, ax = plt.subplots()

sns.lineplot(
    data=df,
    x="Datum",
    y="Temperatur (°C)",
    ax=ax
)

st.pyplot(fig)
```


# Interaktive Funktionen

## Sidebar-Navigation

```python
selection = st.sidebar.radio(
    "Navigation",
    ["Start", "Dashboard"]
)
```

## Slider

```python
tage = st.slider(
    "Zeitraum",
    7,
    90,
    30
)
```

## Mehrfachauswahl

```python
quelle = st.multiselect(
    "Datenquellen",
    ["Temperatur", "Verkehr"]
)
```


# JSON-Dateien automatisch laden

```python
from pathlib import Path

data_path = Path("data/json")

json_files = list(data_path.glob("*.json"))
```


# CSS-Styling

```python
st.markdown(
    """
    <style>
    .card {
        background: white;
        border-radius: 12px;
    }
    </style>
    """,
    unsafe_allow_html=True
)
```


# Geplante Erweiterungen

- Echtzeitdaten
- Wetter-APIs
- KI-Analysen
- Prognosemodelle
- Kartenintegration
- Datenbankanbindung


# Autor

Dipl.-Inf. Thorsten Höke


# Lizenz

Dieses Projekt dient Lern- und Demonstrationszwecken.
