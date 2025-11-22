import mysql from 'mysql2/promise';

export class MySQLConnection {
    constructor(config) {
        this.config = config;
        this.connection = null;
    }

    async connect() {
        this.connection = await mysql.createConnection(this.config);
    }

    async disconnect() {
        if (this.connection) {
            await this.connection.end();
        }
    }

    async createEvent(nom, dateDebut, dateFin, personnesMaximum, lieu) {
        const [result] = await this.connection.execute(
            'CALL CreerEvenement(?, ?, ?, ?, ?, @event_id)',
            [nom, dateDebut, dateFin, personnesMaximum, lieu]
        );
        
        const [idResult] = await this.connection.execute('SELECT @event_id as id');
        return idResult[0].id;
    }

    async createInscription(eventId, prenom, nom, dateInscription = null) {
        await this.connection.execute(
            'CALL InscrirePersonne(?, ?, ?, ?)',
            [eventId, prenom, nom, dateInscription]
        );
    }
}