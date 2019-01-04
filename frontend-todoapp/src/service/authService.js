
export default class AuthService {
    domain = '';
    constructor(domain){
        this.domain = domain;
    }

    setToken(token){
        window.localStorage.setItem('bearer_token', token);
    }    

    getToken() {
        return localStorage.getItem('bearer_token')
    }

    logout() {
        localStorage.removeItem('bearer_token');
    }

    _checkStatus(response) {
        // raises an error in case response status is not a success
        if (response.status >= 200 && response.status < 300) { // Success status lies between 200 to 300
            // console.log(response);
            return response
        } else {
            const errors = new Error(response.statusText)
            errors.message = response
            throw errors
        }
    }
}
