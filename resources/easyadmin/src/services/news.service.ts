import axios from 'axios'
import authHeader from './auth-header'
import Config from '../config'

const BASE_URL = `${Config.host}/api/news`

class NewsService {
    getAll() {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data)
    }
}

export default new NewsService()
