export default class News {
    ID: string
    title: string
    text: string
    date: Date
    expirationDate: Date

    constructor(ID: string, title: string, text: string, date: Date, expirationDate: Date) {
        this.ID = ID
        this.title = title
        this.text = text
        this.date = date
        this.expirationDate = expirationDate
    }
}
