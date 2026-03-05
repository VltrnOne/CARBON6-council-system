/**
 * CARBON[6] Install Code System - API Client
 * Node.js client library for programmatic access
 */

const https = require('https');

class Carbon6InstallAPI {
    constructor(apiKey, baseUrl = 'https://thecarbon6.agency/install/api') {
        this.apiKey = apiKey;
        this.baseUrl = baseUrl;
    }

    async request(method, endpoint, data = null) {
        return new Promise((resolve, reject) => {
            const url = new URL(this.baseUrl + endpoint);

            const options = {
                method: method,
                headers: {
                    'Authorization': `Bearer ${this.apiKey}`,
                    'Content-Type': 'application/json'
                }
            };

            const req = https.request(url, options, (res) => {
                let body = '';

                res.on('data', (chunk) => {
                    body += chunk;
                });

                res.on('end', () => {
                    try {
                        const response = JSON.parse(body);
                        if (res.statusCode >= 200 && res.statusCode < 300) {
                            resolve(response);
                        } else {
                            reject(new Error(response.error || `HTTP ${res.statusCode}`));
                        }
                    } catch (e) {
                        reject(new Error('Invalid JSON response'));
                    }
                });
            });

            req.on('error', reject);

            if (data) {
                req.write(JSON.stringify(data));
            }

            req.end();
        });
    }

    /**
     * Generate a new install code
     * @param {Object} options - Code options
     * @param {string} options.purpose - Purpose label
     * @param {number} options.expiry - Expiry in hours (1-720)
     * @returns {Promise<Object>} Generated code data
     */
    async generateCode({ purpose = 'Generated', expiry = 24 } = {}) {
        return this.request('POST', '/generate', { purpose, expiry });
    }

    /**
     * Check code status
     * @param {string} code - Code to check
     * @returns {Promise<Object>} Code status
     */
    async checkCode(code) {
        return this.request('GET', `/check/${code}`);
    }

    /**
     * Get system statistics
     * @returns {Promise<Object>} Usage statistics
     */
    async getStats() {
        return this.request('GET', '/stats');
    }

    /**
     * List recent codes
     * @param {number} limit - Number of codes to return
     * @returns {Promise<Object>} List of codes
     */
    async listCodes(limit = 20) {
        return this.request('GET', `/list?limit=${limit}`);
    }
}

// Example usage
if (require.main === module) {
    const api = new Carbon6InstallAPI('your-admin-password-here');

    // Generate a code
    api.generateCode({ purpose: 'Demo Client', expiry: 1 })
        .then(result => {
            console.log('✅ Code generated:', result.code);
            console.log('🔗 URL:', result.url);

            // Check the code status
            return api.checkCode(result.code);
        })
        .then(status => {
            console.log('📊 Status:', status.status);
        })
        .catch(err => {
            console.error('❌ Error:', err.message);
        });
}

module.exports = Carbon6InstallAPI;
