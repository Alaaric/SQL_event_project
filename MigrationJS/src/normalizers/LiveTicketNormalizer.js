export class LiveTicketNormalizer {
    accept(data) {
        return data.event && data.start && data.end;
    }

    normalize(data) {
        return {
            nom: data.event,
            dateDebut: new Date(data.start),
            dateFin: new Date(data.end),
            personnesMaximum: data.max || null,
            lieu: data.where
        };
    }

    extractInscriptions(data) {
        return (data.attendees || []).map(attendee => ({
            prenom: attendee.fn,
            nom: attendee.ln
        }));
    }
}