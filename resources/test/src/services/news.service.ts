import axios from 'axios'
import authHeader from './auth-header'
import Config from '../config'
import News from '../models/news'

const BASE_URL = `${Config.host}/api/news`

class NewsService {
    getAll(): Promise<News[]> {
        return axios
            .get(BASE_URL, { headers: authHeader() })
            .then(response => response.data)
    }

    delete(id: string): Promise<void> {
        return axios.delete(`${BASE_URL}/${id}`, { headers: authHeader() })
    }
}

export default new NewsService()
