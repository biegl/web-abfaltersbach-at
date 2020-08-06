import axios from 'axios'
import User from '@/models/user'

const API_URL = 'http://localhost:8000/api'

class AuthService {
  login(user: User) {
    return axios
      .post(`${API_URL}/login`, {
        email: user.username,
        password: user.password,
      })
      .then(response => {
        if (response.data.user.api_token) {
          localStorage.setItem('user', JSON.stringify(response.data))
        }

        return response.data
      })
  }

  logout() {
    localStorage.removeItem('user')
  }
}

export default new AuthService()
