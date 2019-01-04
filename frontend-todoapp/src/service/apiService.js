import { create } from 'apisauce';
import Auth from './authService';

export const AuthService = new Auth(process.env.BASE_API);
const url = process.env.VUE_APP_ROOT_API;

const api = create({
    baseURL: url,
});

api.addAsyncRequestTransform((request) => async () => {
    const token = AuthService.getToken();

    request.headers['Authorization'] = `Bearer ${token}`;
    request.headers['Access-Control-Allow-Origin'] = '*';
});

api.addResponseTransform((response) => {
    if (response.status<200 || response.status>300) throw response;    
});

export default api;
