import BaseObject from "./base";

export default class Activity implements BaseObject {
    id: string;
    log_name: string;
    description: string;
    subject_type: string;
    subject_id: number;
    causer_type: string;
    causer_id: number;
    properties: string;
    created_at: Date;
}
