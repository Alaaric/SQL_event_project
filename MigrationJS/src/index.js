import dotenv from 'dotenv';
import { MongoConnection } from './database/MongoConnection.js';
import { MySQLConnection } from './database/MySQLConnection.js';
import { EventMigrator } from './migrator/EventMigrator.js';

dotenv.config();

class MigrationApp {
    constructor() {
        this.mongoConnection = new MongoConnection(
            process.env.MONGODB_URI,
            process.env.MONGODB_DATABASE
        );
        
        this.mysqlConnection = new MySQLConnection({
            host: process.env.MYSQL_HOST,
            user: process.env.MYSQL_USER,
            password: process.env.MYSQL_PASSWORD,
            database: process.env.MYSQL_DATABASE
        });
        
        this.migrator = new EventMigrator(this.mongoConnection, this.mysqlConnection);
    }

    async run() {
        try {
            await this.mongoConnection.connect();
            await this.mysqlConnection.connect();
            
            const result = await this.migrator.migrate();
            
            return result;
            
        } catch (error) {
            console.error('Error :', error.message);
            process.exit(1);
        } finally {
            await this.mongoConnection.disconnect();
            await this.mysqlConnection.disconnect();
        }
    }
}

const app = new MigrationApp();
app.run().then(result => {
    console.log(`Migration terminée : ${result.migrated} événements migrés.`);
    process.exit(result.errors.length > 0 ? 1 : 0);
}).catch(error => {
    console.error(error);
    process.exit(1);
});