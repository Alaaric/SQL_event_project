import { MongoClient } from 'mongodb';

export class MongoConnection {
    constructor(uri, database) {
        this.uri = uri;
        this.database = database;
        this.client = null;
        this.db = null;
    }

    async connect() {
        this.client = new MongoClient(this.uri);
        await this.client.connect();
        this.db = this.client.db(this.database);
    }

    async disconnect() {
        if (this.client) {
            await this.client.close();
        }
    }

    getEventsCollection() {
        return this.db.collection('events');
    }

    async getAllEvents() {
        const collection = this.getEventsCollection();
        return await collection.find({}).toArray();
    }
}