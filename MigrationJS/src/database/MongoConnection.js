import { MongoClient } from 'mongodb';

export class MongoConnection {
    static COLLECTION_NAME = 'events';

    constructor(uri, database) {
        this.uri = uri;
        this.database = database;
        this.client = null;
        this.db = null;
        this.eventsCollection = null;
    }

    async connect() {
        this.client = new MongoClient(this.uri);
        await this.client.connect();
        this.db = this.client.db(this.database);
        this.eventsCollection = this.db.collection(MongoConnection.COLLECTION_NAME);
    }

    async disconnect() {
        if (this.client) {
            await this.client.close();
        }
    }

    async getAllEvents() {
        return await this.eventsCollection.find({ migrated: { $ne: true } }).toArray();
    }

    async markAsMigrated(eventId) {
        await this.eventsCollection.updateOne(
            { _id: eventId },
            { $set: { migrated: true } }
        );
    }
}