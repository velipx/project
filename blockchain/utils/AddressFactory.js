const bitcoin = require('bitcoinjs-lib');
const ecc = require('tiny-secp256k1');
const ECPairFactory = require('ecpair').default;
const { generateMnemonic, mnemonicToSeedSync } = require('bip39');
const BIP32Factory = require('bip32').default;
const ethers = require('ethers');
const { randomBytes } = require('crypto');

// Initialize factories
const ECPair = ECPairFactory(ecc);
const bip32 = BIP32Factory(ecc);

// AddressFactory class to select the correct address generator
class AddressFactory {
    static getAddressGenerator(crypto) {
        if (crypto === 'BTC') {
            return new BitcoinAddressGenerator();
        } else if (crypto === 'ETH') {
            return new EthereumAddressGenerator();
        } else {
            throw new Error('Unsupported cryptocurrency');
        }
    }
}

// Bitcoin address generator using updated bitcoinjs-lib API
class BitcoinAddressGenerator {
    generateAddress() {
        try {
            console.log('Generating Bitcoin address...');

            // Generate mnemonic
            const mnemonic = generateMnemonic();
            const seed = mnemonicToSeedSync(mnemonic);
            // Derive the master node using BIP32
            const root = bip32.fromSeed(seed);
            const keyPair = ECPair.fromPrivateKey(root.derivePath("m/44'/0'/0'/0/0").privateKey);

            const { address } = bitcoin.payments.p2pkh({ pubkey: keyPair.publicKey });
            console.log('Bitcoin address:', address);
            return {
                address: address,
                privateKey: keyPair.toWIF() // Convert private key to Wallet Import Format (WIF)
            };
        } catch (error) {
            console.error('Error generating Bitcoin address:', error);
            throw error;
        }
    }
}

// Ethereum address generator
class EthereumAddressGenerator {
    generateAddress() {
        console.log('Generating Ethereum address...');
        const wallet = ethers.Wallet.createRandom(); // Create random Ethereum wallet
        console.log('Ethereum wallet:', wallet);
        return {
            address: wallet.address,
            privateKey: wallet.privateKey
        };
    }
}

module.exports = AddressFactory;
