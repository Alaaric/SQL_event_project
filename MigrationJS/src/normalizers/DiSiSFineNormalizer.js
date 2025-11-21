export class DISISFineNormalizer {
    accept(data) {
        return data.e_name && data.e_start && data.e_finish;
    }

    normalize(data) {
        return {
            nom: data.e_name,
            dateDebut: new Date(data.e_start),
            dateFin: new Date(data.e_finish),
            personnesMaximum: data.e_attendees_max || null,
            lieu: data.e_location
        };
    }

    extractInscriptions(data) {
        if (!data.attendees) return [];
        try {
            const attendeesList = JSON.parse(data.attendees);
            return attendeesList.map(attendee => ({
                prenom: attendee[0],
                nom: attendee[1]
            }));
        } catch {
            return [];
        }
    }
}