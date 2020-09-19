import { Role } from "./../helpers/role";
import BaseObject from "./base";

export default class User implements BaseObject {
    id: string;
    username: string;
    email: string;
    password: string;
    api_token: string;
    role: number;

    static init(obj: Partial<User>): User {
        const user = new User();
        user.username = obj.username;
        user.email = obj.email;
        user.password = obj.password;
        user.role = obj.role;
        return user
    }
}
