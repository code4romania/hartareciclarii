export const CONSTANTS =
{
	API_DOMAIN: `${import.meta.env.VITE_API_URL}`,
	APP_DOMAIN: `${import.meta.env.VITE_APP_URL}`,
	DEFAULT_ITEMS_PER_PAGE: 12,
	NOMINATIM_URL_DETAILS: `https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&zoom=18&addressdetails=1`,
	NOMINATIM_URL_POINTS: `https://nominatim.openstreetmap.org/search?format=json&q={search}&addressdetails=1`,
	DEFAULT_LOCATION:{
		LATITUDE: 46.755504,
		LONGITUDE: 23.5787266
	},
	EXTERNAL_URLS:{
		GUIDE: 'https://hartareciclarii.ro/ce-si-cum-reciclez/#/category/all',
		FAQ: 'https://hartareciclarii.ro/despre-proiect/intrebari-frecvente/'
	},
	ROUTES:
    {
		AUTH:
        {
			LOGIN: `/auth/login`,
			LOGOUT: `/auth/logout`,
			REFRESH: `/user/refresh`,
			RECOVER: `/user/recover`,
			RECOVER_CONFIRM: `/user/recover-confirm`,
		},
		USER:
        {
			PROFILE:
            {
				INFO: `/user/profile`,
				REGISTER: `/user/register`,
				POINTS: `/user/points`,
			}
		},
        STATIC:
        {
            FILTERS:
            {
                FILTERS: `/static/filters`
            },
            NOMENCLATURES: {
                GET: `/map/nomenclatures`
            },
            IMAGE: `/static/image`
        },
        MAP:
        {
            POINTS:
            {
                INFO: `/map/points`,
                DETAILS: `/map/point/{id}`,
                CREATE: `/map/points`,
                REPORT: `/report/problem/{id}`,
            }
        },
	},
    LABELS:
    {
        SIDEBAR:
        {
            SEARCH_POINT_LABEL: `Caută un punct`,
            SEARCH_MATERIAL_LABEL: `Caută material`,
            SEARCH_POINT_PLACEHOLDER: `Exemplu căutare`,
            SERVICE_TYPE_LABEL: `Tip serviciu`,
            MATERIAL_TYPE_LABEL: `Material colectat selectiv`,
            COLLECTION_POINT_TYPE_LABEL: `Tip punct de colectare`,
            CLEAR_FILTERS_LABEL: `Șterge filtre`,
			SEARCH: `Caută`,
			FILTERS: `Filtre`,
			FILTERS_TITLE: `FILTREAZĂ REZULTATELE`,
            RESULTS: `Rezultate`,
            NO_RESULTS_FOUND_FIRST_PART: `Nu a fost gasit niciun rezultat pentru`,
            NO_RESULTS_FOUND_SECOND_PART: `Folositi un alt termen de cautare`,
            SEE_ALL_POINTS: `Vezi toate punctele`
        },
		DAYS_OF_WEEK:
		{
			monday: `Luni`,
			tuesday: `Marți`,
			wednesday: `Miercuri`,
			thursday: `Joi`,
			friday: `Vineri`,
			saturday: `Sâmbătă`,
			sunday: `Duminică`,
		},
		TOP_MENU:
		{
			ADD_POINT: `Adaugă un punct`,
			DICTIONARY: `Dicționar`,
			DICTIONARY_RECYCLING: `Dicționar reciclare`,
			GUIDE_A_Z: `Ghiz A-Z`,
			FAQ: `FAQ`,
			MY_ACCOUNT: `Contul meu`,
			LOGOUT: `Logout`,
			MY_PROFILE: `Profilul meu`,
		},
		LOCATION:
		{
			NOTICE: `Serviciile de localizare nu sunt pornite pe acest dispozitiv. Pentru o localizare mai corectă, porniți serviciul localizare din setări.`,
			SETTINGS: `SETĂRI`,
		},
		AUTH:
		{
			EMAIL: `Email`,
			EMAIL_PLACEHOLDER: `Adresa de email`,
			PASSWORD: `Parola`,
			PASSWORD_PLACEHOLDER: `Parola`,
			RECOVER: `Am uitat parola`,
			NEXT_STEP: `Următorul pas`,
			LOGIN_BUTTON: `Intră în cont`,
			LOGIN_FORM_TITLE: `Intră în cont`,
			RECOVER_FORM_TITLE: `Recuperare parolă`,
			REGISTER_FORM_TITLE: `Crează-ți un cont`,
			REGISTER_LABEL: `Nu ai cont Harta Reciclării?`,
			REGISTER_LABEL_LINK: `Crează-ți unul acum`,
			ERROR: `Email sau parolă invalide!`,
		},
        ADD_POINT: {
            TITLE: `Adauga un nou punct pe harta`,
            NEXT_STEP: `Urmatorul pas`,
            FINISH_STEPS: `Adauga punct`,
            SUCCESS_MESSAGE: `Punct adaugat cu succes. Un moderator Harta Reciclarii va valida informatia in cel mai scurt timp`,
            CANCEL: `Renunta`,
            BACK: `Inapoi`,
            FIRST_STEP: {
                SERVICE_TYPE_PLACEHOLDER: `Selecteaza un tip de punct`,
                PLACE_PIN: `Plaseaza pinul`,
                SUBTITLE: `Pasul 1/3 - Tip si locatie`,
                EXACT_ADDRESS_LABEL: `Adresa exacta`,
                EXACT_ADDRESS_PLACEHOLDER: `Introdu adresa exacta a punctului`,
                ADJUST_POINT_ON_MAP: `Ajusteaza punctul pe harta`,
                SERVICE_TYPE_REQUIRED: `Tip serviciu este obligatoriu!`,
                ADDRESS_REQUIRED: `Adresa este obligatorie!`,
                POINT_REQUIRED: `Pinul pe harta este ogligatoriu!!`,
                ADDRESS_NOT_FOUND: `Nu s-a putut gasi adresa!`
            },
            SECOND_STEP: {
                SUBTITLE: `Pasul 2/3 - Detalii punct`,
                POINT_TYPE: `Tip punct`,
                POINT_TYPE_REQUIRED: `Tip punct este obligatoriu!`,
                COLLECTED_MATERIALS: `Materiale colectate`,
                COLLECTED_MATERIALS_REQUIRED: `Materiale colectate este obligatoriu!`,
                ADMINISTRATION: `Administrat de`,
                ADMINISTRATION_PLACEHOLDER: `Institutia care administreaza punctul`,
                UNKNOWN_ADMINISTRATION: `Nu stiu`,
                PROGRAM: `Orar`,
                CLOSED: `Inchis`,
                DAYS: `Zile`,
                HOURS: `Ore`,
                PROGRAM_PLACEHOLDER: `Zilele si intervalul orar de funtionare`,
                UNKNOWN_PROGRAM: `Nu stiu`,
                OFFERS_MONEY: `Ofera bani?`,
                OFFERS_SHIP: `Ofera transport?`,
                OBSERVATIONS: `Observatii`,
                OBSERVATIONS_PLACEHOLDER: `Alte detalii despre locatie sau despre modul de oferire al serviciului`,
                ADD_PICTURE: `Adauga poze`,
                ADD_PICTURE_SUBTITLE: `Orice fel de poze sugestive sau utile pentru a identifica locatia sau punctul adaugat`,
                WEBSITE: `Website`,
                EMAIL: `Email`,
                PHONE: `Phone`
            },
            THIRD_STEP: {
                SUBTITLE: `Pasul 3/3 - Confirma informatiile`,
                ADDRESS: `Adresa`,
                PROGRAM: `Program`,
                COLLECTED_MATERIALS: `Materiale colectate`,
                OBSERVATIONS: `Observatii`,
                OFFERS_SHIP: `Ofera transport`,
                DOESNT_OFFERS_SHIP: `Nu ofera transport`,
                OFFERS_MONEY: `Ofera bani`,
                DOESNT_OFFERS_MONEY: `NU ofera bani`,
                WEBSITE: `Website`,
                EMAIL: `Email`,
                PHONE: `Phone`
            }
        },
		API:
		{
			invalid_credentials: `Email sau parolă incorecte!`
		},
		POINT_DETAILS:
		{
			CLOSED: `Închis`,
			ADDRESS_LABEL: `Adresa`,
			SCHEDULE_LABEL: `Program`,
			REPORT_PROBLEM_LABEL: `Raporteazǎ o problemǎ`,
			MATERIALS_LABEL: `Materiale colectate`,
			NOTES_LABEL: `Observații`,
			TRANSPORT_LABEL: `Oferǎ transport`,
			MONEY_LABEL: `Oferǎ bani`,
			WEBSITE_LABEL: `Website`,
			PHONE_LABEL: `Telefon`,
			SHARE_LABEL: `Distribuie`,
			COPY_LABEL: `Copiază`,
			LINK_COPIED: `URL-ul a fost copiat în clipboard!`,
		},
		REPORT_PROBLEM:
		{
			TITLE: 'Raportează o problemă',
			CANCEL: 'Renunță',
			BACK: 'Înapoi',
			NEXT_STEP: 'Următorul',
			FINISH_STEP: 'Trimite sugestia',
			FIRST_STEP:
			{
				SUBTITLE: "Ce tip de problemă ai identificat?",
				ISSUE_TYPE_REQUIRED: `Tipul de problemǎ este obligatoriu!`,
			},
			ADDRESS_STEP:
			{
				TITLE: "Adresa nu este corectă",
				SUBTITLE: "Sugereaza o noua adresa sau locatie mai jos, prin modificarea adresei existente și/sau ajustarea punctului pe hartă.",
				EXACT_ADDRESS_LABEL: `Adresa exacta`,
				EXACT_ADDRESS_PLACEHOLDER: `Introdu adresa exacta a punctului`,
				ADJUST_POINT_ON_MAP: `Ajusteaza punctul pe harta`,
				PLACE_PIN: `Plaseaza pinul`,
				ADDRESS_REQUIRED: `Adresa este obligatorie!`,
				POINT_REQUIRED: `Pinul pe harta este ogligatoriu!!`,
				ADDRESS_NOT_FOUND: `Nu s-a putut gasi adresa!`,
				SUCCESS:
				{
					TITLE: `Vă mulțumim pentru sesizare!`,
					SUB_TITLE: `Un coleg Harta Reciclării a primit problema raportată și va actualiza în cel mai scurt timp punctul pe hartă.`,
				}
			},
			SCHEDULE_STEP:
			{
				TITLE: "Programul nu este corect",
				SUBTITLE: "Vă rugăm să ne descrieți situația cu cât mai multe detalii, pentru a ne oferi posibilitatea de a corecta informația de pe hartă.",
				DESCRIPTION_NOT_FOUND: `Vǎ rugam completați descrierea problemei!`,
				SUCCESS:
				{
					TITLE: `Vă mulțumim pentru sesizare!`,
					SUB_TITLE: `Un coleg Harta Reciclării a primit problema raportată și va actualiza în cel mai scurt timp punctul pe hartă.`,
				}
			},
			CONTAINER_STEP:
			{
				TITLE: "Containerul nu funcționează",
				SUBTITLE: "Vă rugăm să ne descrieți situația cu cât mai multe detalii, pentru a ne oferi posibilitatea de a corecta informația de pe hartă.",
				DESCRIPTION_NOT_FOUND: `Vǎ rugam completați descrierea problemei!`,
				ADD_PHOTOS_TITLE: "Orice fel de poze sugestive sau utile pentru a susține descrierea problemei",
				ADD_PHOTOS_LABEL: "Adaugă poze",
				SUCCESS:
					{
						TITLE: `Vă mulțumim pentru sesizare!`,
						SUB_TITLE: `Un coleg Harta Reciclării a primit problema raportată și va actualiza în cel mai scurt timp punctul pe hartă.`,
						SUB_TITLE_UNDER_IMAGE: `Între timp, vă sugerăm să contactați administratorul acestei locații, pentru a-l informa despre problemă. Astfel vă puteți asigura că problema va fi și rezolvată.`,
						WEBSITE: `Website`,
						PHONE: `Telefon`,
					}
			},
			OTHER_PROBLEM_STEP:
			{
				TITLE: "Altă problemă",
				SUBTITLE: "Vă rugăm să ne descrieți situația cu cât mai multe detalii, pentru a ne oferi posibilitatea de a corecta informația de pe hartă.",
				DESCRIPTION_NOT_FOUND: `Vǎ rugam completați descrierea problemei!`,
				ADD_PHOTOS_TITLE: "Orice fel de poze sugestive sau utile pentru a susține descrierea problemei",
				ADD_PHOTOS_LABEL: "Adaugă poze",
				SUCCESS:
					{
						TITLE: `Vă mulțumim pentru sesizare!`,
						SUB_TITLE: `Un coleg Harta Reciclării a primit problema raportată și va actualiza în cel mai scurt timp punctul pe hartă.`,
						SUB_TITLE_UNDER_IMAGE: `Între timp, vă sugerăm să contactați administratorul acestei locații, pentru a-l informa despre problemă. Astfel vă puteți asigura că problema va fi și rezolvată.`,
						WEBSITE: `Website`,
						PHONE: `Telefon`,
					}
			},
			TAKEOVER_STEP:
			{
				TITLE: "S-a refuzat preluarea",
				SUBTITLE: "Care a fost motivul refuzului?",
				CAPTION: "Vă rugăm să ne descrieți situația cu cât mai multe detalii, pentru a ne oferi posibilitatea de a corecta informația de pe hartă.",
				DESCRIPTION_NOT_FOUND: `Vǎ rugam completați descrierea problemei!`,
				SUCCESS:
					{
						TITLE: `Vă mulțumim pentru sesizare!`,
						SUB_TITLE: `Un coleg Harta Reciclării a primit problema raportată și va actualiza în cel mai scurt timp punctul pe hartă.`,
						SUB_TITLE_UNDER_IMAGE: `Între timp, puteți citi mai multe despre ceea ce puteți face atunci când vi se refuză un deșeu să fie preluat, sau cum puteți evita astfel de situații`,
						GUIDE_LABEL: 'Ghid Reciclare'
					}
			},
			MATERIALS_OPTIONS_STEP:
			{
				TITLE: "Materialele nu sunt corecte",
				SUBTITLE: "Care este problema sesizată legată de materiale?",
				OPTIONS_NOT_FOUND: `Trebuie selectatǎ cel puțin o opțiune!`
			},
			MATERIALS_NOT_COLLECTED_STEP:
			{
				TITLE: "Nu se colectează unele materiale",
				SUBTITLE: "Care dintre următoarele materiale ați identificat că NU se colectează în realitate?",
				MATERIALS_NOT_FOUND: "Trebuie selectat cel puțin un material!",
			},
			MATERIALS_MISSING_STEP:
			{
				TITLE: "Lipsesc materiale din descriere",
				SUBTITLE: "Care dintre următoarele materiale ați identificat că lipsesc din descrierea locației?",
				MATERIALS_NOT_FOUND: "Trebuie selectat cel puțin un material!",
			},
			MATERIALS_OTHER_STEP:
			{
				TITLE: "Altă problemă",
				SUBTITLE: "Vă rugăm să ne descrieți situația cu cât mai multe detalii, pentru a ne oferi posibilitatea de a corecta informația de pe hartă.",
				ADD_PHOTOS_TITLE: "Orice fel de poze sugestive sau utile pentru a susține descrierea problemei",
				ADD_PHOTOS_LABEL: "Adaugă poze",
			}
		}
    }
};
