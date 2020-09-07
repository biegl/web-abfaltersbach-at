import { Role } from "./../helpers/role";

export default class User {
    id: number;
    username: string;
    email: string;
    password: string;
    api_token: string;
    role: number;

    constructor(username: string, email: string, password: string) {
        this.username = username;
        this.email = email;
        this.password = password;
        this.role = Role.User;
    }
}
