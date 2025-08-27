# Roundnet League Manager
Web aplikacija za praćenje liga u RoundNetu

Malkice sam zakomplikovao projekat jer sam hteo da napravim nešto što ćemo zapravo koristiti, pa ću Vam sada navesti osnovne zahteve projekta da Vam bude lakše da pregledate:
- Pokrenite sve iz "sql.sql"
- Na dugme "Prijava" se možete prijaviti ili napraviti profil kao korisnik
    + obrada POST zahteva
    + rad sa cookies ("username_reg" i "remember_me") i sesijama
    + zaštita od XSS napada
    + rad sa MySQL bazom podataka
- Kada ste ulogovani kao korisnik, možete menjati profil i Vaše mečeve
- Takodje, možete pogledati i sve lige. Odabrati neku od njih i pogledati kola i mečeve od iste
    + obrada GET zahteva
- Kada ste ulogovani kao admin, dodatno možete menjati lige, kola i mečeve
    + obrada GET zahteva
    + rad sa MySQL bazom podataka