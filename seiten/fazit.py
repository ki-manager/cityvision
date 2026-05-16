import streamlit as st
from utils.components import card

def show():
    st.title("Fazit")

    card(
        "Ausblick",
        "Die modulare Architektur erlaubt es, CityVision schrittweise "
        "zu einer vollwertigen Analyseplattform auszubauen." 
        
    )

    c1, c2 = st.columns(2)

    c3, c4 = st.columns(2)

    with c1:
        card(
            "Erweiterte Datenquellen",
            "Integration zusätzlicher Daten wie Verkehrs-, Wetter-, Umwelt- und Energiedaten zur umfassenderen Analyse urbaner Entwicklungen."
        )

    with c2:
        card(
            "KI & Machine Learning",
            "Einsatz intelligenter Algorithmen zur Erkennung von Mustern, Prognosen und Auffälligkeiten in Echtzeitdaten."
        )

    with c3:
        card(
            "Echtzeit & IoT",
            "Einbindung von Live-Daten und IoT-Sensoren zur Überwachung von Verkehr, Umwelt und Infrastruktur."
        )

    with c4:
        card(
            "Smart-City-Plattform",
            "Ausbau zu einer skalierbaren Plattform mit interaktiven Dashboards und datenbasierten Entscheidungshilfen."
        )
