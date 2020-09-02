import authService from './auth.service';

export default function authHeader() {
    const user = authService.currentUser;

    if (user && user.api_token) {
        return { Authorization: `Bearer ${user.api_token}` };
    } else {
        return {};
    }
}
