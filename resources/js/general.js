import axios, {HttpStatusCode} from 'axios';
import {CONSTANTS} from './constants';
import _ from 'lodash';
import moment from 'moment';

export function allFilters(force = false)
{
    if (process.env.NODE_ENV !== 'development')
    {
        if (force)
        {
            if (localStorage.getItem('allFilters'))
            {
                return;
            }
        }
    }

    axios
        .get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.STATIC.FILTERS.FILTERS)
        .then((response) =>
        {

            const allFilters = _.get(response, 'data');

            localStorage.setItem(
                'allFilters',
                JSON.stringify(allFilters)
            );
        });
}

export function checkAuthRoutes(router)
{
    router.beforeEach(async (toPath, fromPath) =>
    {
        const session = localStorage.getItem('userSession');
        if (_.get(toPath, 'meta.requiresAuth', false))
        {
            if (!session)
            {
                localStorage.setItem(
                    'previousRoute',
                    toPath.fullPath
                );
                router.push('/login');
            }
            const parsedSession = JSON.parse(session);
            if (_.isNull(session))
            {
                return;
            }

            const sessionExpirationTime = moment(parsedSession.expires);
            const now = moment();

            if (now.isAfter(sessionExpirationTime))
            {
                localStorage.removeItem('userSession');
                router.push('/login');
            }
            return;
        }

        if (session)
        {
            const sessionExpirationTime = moment(JSON.parse(session).expires);
            const now = moment();
            if (now.isBefore(sessionExpirationTime))
            {
                let redirectTo = '/dashboard';
                if (fromPath.name)
                {
                    redirectTo = fromPath.fullPath;
                }

                //router.push(redirectTo);
            }

            if (toPath.fullPath === '/')
            {
                router.push('/login');
            }
        }
        if (toPath.fullPath === '/')
        {
            location.href = import.meta.env.VITE_MY_CONNECTOR_SITE;
        }
    });
}

export function checkUnauthorizedResponse(router)
{
    axios.interceptors.response.use(response =>
    {
        return response;
    }, error =>
    {
        if (error.response.status === HttpStatusCode.Unauthorized)
        {
            localStorage.removeItem('userSession');
            throw error;
        }
        if (error.response.status === HttpStatusCode.BadRequest)
        {
            throw error;
        }
        return error;
    });
}

export function addAuthToken()
{
    axios.interceptors.request.use(
        (config) =>
        {
            config.headers['Accept'] = 'application/json';
            const userToken = JSON.parse(localStorage.getItem('userToken'));

            if (_.get(userToken, 'token', false))
            {
                config.headers['Authorization'] = `Bearer ${userToken.token}`;
            }

            return config;
        },

        (error) =>
        {
            return Promise.reject(error);
        }
    );
}

export async function getUserProfile()
{
    let userInfo = {};
    const session = localStorage.getItem('userSession');
    if (session)
    {
        await axios
            .get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.USER.PROFILE.INFO)
            .then((response) =>
            {
                userInfo = _.get(response, 'data.data', {});
            }).catch((err) =>
        {
        });
    }

    return userInfo;
}
