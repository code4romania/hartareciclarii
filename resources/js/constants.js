export const CONSTANTS =
{
	API_DOMAIN: `${import.meta.env.VITE_API_URL}`,
	DEFAULT_ITEMS_PER_PAGE: 12,
	DEFAULT_LOCATION:{
		LATITUDE: 46.755504,
		LONGITUDE: 23.5787266
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
            }
        },
        MAP:
        {
            POINTS:
            {
                INFO: `/map/points`,
                DETAILS: `/map/point/{id}`,
                CREATE: `/map/point`,
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
		API:
		{
			invalid_credentials: `Email sau parolă incorecte!`
		},
    }
};
