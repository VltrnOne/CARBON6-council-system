"""
CARBON[6] Install Code System - Python API Client
Easy programmatic access to code generation and management
"""

import requests
from typing import Optional, Dict, List


class Carbon6InstallAPI:
    """Client for CARBON[6] Install Code System API"""

    def __init__(self, api_key: str, base_url: str = "https://thecarbon6.agency/install/api"):
        """
        Initialize API client

        Args:
            api_key: Admin password for authentication
            base_url: Base URL for API (default: production)
        """
        self.api_key = api_key
        self.base_url = base_url
        self.headers = {
            "Authorization": f"Bearer {api_key}",
            "Content-Type": "application/json"
        }

    def generate_code(self, purpose: str = "Generated", expiry: int = 24) -> Dict:
        """
        Generate a new install code

        Args:
            purpose: Purpose label for tracking
            expiry: Expiry time in hours (1-720)

        Returns:
            Dict with code, url, and expiry information

        Example:
            >>> api = Carbon6InstallAPI("your-password")
            >>> result = api.generate_code(purpose="Client Demo", expiry=1)
            >>> print(result['url'])
        """
        response = requests.post(
            f"{self.base_url}/generate",
            json={"purpose": purpose, "expiry": expiry},
            headers=self.headers
        )
        response.raise_for_status()
        return response.json()

    def check_code(self, code: str) -> Dict:
        """
        Check code status

        Args:
            code: Code to check (e.g., "K7QW3M9P")

        Returns:
            Dict with status, creation time, and usage info
        """
        response = requests.get(
            f"{self.base_url}/check/{code}",
            headers=self.headers
        )
        response.raise_for_status()
        return response.json()

    def get_stats(self) -> Dict:
        """
        Get system statistics

        Returns:
            Dict with total, active, used, and expired counts
        """
        response = requests.get(
            f"{self.base_url}/stats",
            headers=self.headers
        )
        response.raise_for_status()
        return response.json()

    def list_codes(self, limit: int = 20) -> List[Dict]:
        """
        List recent codes

        Args:
            limit: Number of codes to return

        Returns:
            List of code dictionaries
        """
        response = requests.get(
            f"{self.base_url}/list?limit={limit}",
            headers=self.headers
        )
        response.raise_for_status()
        return response.json()['codes']


# Example usage
if __name__ == "__main__":
    # Initialize client
    api = Carbon6InstallAPI("your-admin-password-here")

    # Generate a code
    result = api.generate_code(purpose="Demo Client", expiry=1)
    print(f"✅ Code generated: {result['code']}")
    print(f"🔗 URL: {result['url']}")

    # Check status
    status = api.check_code(result['code'])
    print(f"📊 Status: {status['status']}")

    # Get stats
    stats = api.get_stats()
    print(f"📈 Total codes: {stats['stats']['total_generated']}")
    print(f"📈 Active: {stats['stats']['active']}")
