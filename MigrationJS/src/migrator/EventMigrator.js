import { LiveTicketNormalizer } from '../normalizers/LiveTicketNormalizer.js';
import { TrueGisterNormalizer } from '../normalizers/TrueGisterNormalizer.js';
import { DISISFineNormalizer } from '../normalizers/DISISFineNormalizer.js';

export class EventMigrator {
    constructor(mongoConnection, mysqlConnection) {
        this.mongoConnection = mongoConnection;
        this.mysqlConnection = mysqlConnection;
        this.normalizers = [
            new LiveTicketNormalizer(),
            new TrueGisterNormalizer(), 
            new DISISFineNormalizer(),
        ];
    }

    findNormalizer(eventData) {
        return this.normalizers.find(normalizer => normalizer.accept(eventData));
    }

    async migrateEvent(rawEvent) {
        const normalizer = this.findNormalizer(rawEvent);
        const normalizedEvent = normalizer.normalize(rawEvent);
        const inscriptions = normalizer.extractInscriptions(rawEvent);

        const eventId = await this.mysqlConnection.createEvent(
            normalizedEvent.nom,
            normalizedEvent.dateDebut.toISOString().slice(0, 19).replace('T', ' '),
            normalizedEvent.dateFin.toISOString().slice(0, 19).replace('T', ' '),
            normalizedEvent.personnesMaximum,
            normalizedEvent.lieu
        );

        inscriptions.forEach(async inscription => {
            try {
                await this.mysqlConnection.createInscription(
                    eventId,
                    inscription.prenom,
                    inscription.nom
                );
            } catch (error) {
                console.warn(`Inscription error:`, error.message);
            }
        });

        return eventId;
    }

    async migrate() {
        
        const events = await this.mongoConnection.getAllEvents();
        
        let migratedEventCount = 0;
        const errors = [];
        
        for (const rawEvent of events) {
            try {
                await this.migrateEvent(rawEvent);
                migratedEventCount++;
            } catch (error) {
                const errorMsg = `Erreur événement "${rawEvent.nom || rawEvent.name || 'inconnu'}": ${error.message}`;
                errors.push(errorMsg);
                console.error(errorMsg);
            }
        }
        
        return { migrated: migratedEventCount, errors };
    }
}