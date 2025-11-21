export class BaseNormalizer {
    canHandle(data) {
        throw new Error('canHandle method must be implemented');
    }

    normalize(data) {
        throw new Error('normalize method must be implemented');
    }

    extractInscriptions(data) {
        return [];
    }
}