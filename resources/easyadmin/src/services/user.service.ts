import axios from 'axios';
import authHeader from './auth-header';

const BASE_URL = '/api/users/';

class UserService {
  getAll() {
    return axios.get(BASE_URL, { headers: authHeader() });
  }
}

export default new UserService();
