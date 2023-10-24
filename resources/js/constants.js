export const CONSTANTS =
{
	API_DOMAIN: `${import.meta.env.VITE_API_URL}`,
	DEFAULT_ITEMS_PER_PAGE: 12,
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
            SEARCH_POINT_PLACEHOLDER: `Exemplu căutare`,
            SERVICE_TYPE_LABEL: `Tip serviciu`,
            CLEAR_FILTERS_LABEL: `Șterge filtre`,
			SEARCH: `Caută`,
			FILTERS: `Filtre`,
			FILTERS_TITLE: `FILTREAZĂ REZULTATELE`,
        },
		TOP_MENU:
		{
			ADD_POINT: `Adaugă un punct`,
			DICTIONARY: `Dicționar`,
			DICTIONARY_RECYCLING: `Dicționar reciclare`,
			GUIDE_A_Z: `Ghiz A-Z`,
			FAQ: `FAQ`,
			MY_ACCOUNT: `Contul meu`
		},
		LOCATION:
		{
			NOTICE: `Serviciile de localizare nu sunt pornite pe acest dispozitiv. Pentru o localizare mai corectă, porniți serviciul localizare din setări.`,
			SETTINGS: `SETĂRI`,
		},
    }
};