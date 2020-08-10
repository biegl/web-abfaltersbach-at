import axios from 'axios'
import User from '@/models/user'
import Config from '../config';

const BASE_URL = Config.host + '/api'
class AuthService {
    login(user: User) {
        return axios
            .post(`${BASE_URL}/login`, {
                email: user.username,
                password: user.password,
            })
            .then(response => {
                if (response.data.user.api_token) {
                    localStorage.setItem('user', JSON.stringify(response.data.user))
                }

                return response.data.user
            })
    }

    logout() {
        localStorage.removeItem('user')
    }
}

export default new AuthService()
