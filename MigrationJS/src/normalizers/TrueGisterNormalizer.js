export class TrueGisterNormalizer {
    accept(data) {
        return data.results && data.results[0] && data.results[0].event;
    }

    normalize(data) {
        const event = data.results[0].event;
        return {
            nom: event.event_name,
            dateDebut: new Date(event.event_begin),
            dateFin: new Date(event.event_finish),
            personnesMaximum: null,
            lieu: event.event_where
        };
    }

    extractInscriptions(data) {
        return (data.results[0].attendees || []).map(attendee => ({
            prenom: attendee.attendee_1,
            nom: attendee.attendee_2
        }));
    }
}