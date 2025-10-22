import time
import random
from datetime import datetime
from influxdb import InfluxDBClient # Pustaka untuk InfluxDB 1.x

# --- Konfigurasi InfluxDB (Diperbarui) ---
INFLUXDB_HOST = 'localhost' 
INFLUXDB_PORT = 8086
INFLUXDB_USER = 'grafana'
INFLUXDB_PASSWORD = 'solar'
# Diperbarui sesuai INFLUX_DB=monitoring_sensor
INFLUXDB_DATABASE = 'monitoring_sensor' 

# Tag unik untuk mengidentifikasi sumber data (sesuai "flag")
DEVICE_TAG = "c6:cc:e7:0d:69:5b"
MAC_ADDRESS = "c6:cc:e7:0d:69:5b"  # MAC address yang digunakan oleh semua controller (sesuai SiteSeeder)

# --- Skema Data (Measurements dan Fields) untuk semua Controller ---
MEASUREMENTS_SCHEMA = {
    # Untuk EpeverController - epever_data measurement
    "epever_data": {
        "pv_voltage": "float", "battery_voltage": "float", "load_voltage": "float",
        "pv_current": "float", "battery_current": "float", "load_current": "float",
        "pv_power": "float", "battery_power": "float", "load_power": "float",
        "battery_soc": "float", "battery_temperature": "float", "device_temperature": "float",
        "charging_status": "integer", "load_status": "integer", "system_status": "integer"
    },
    
    # Untuk ArduinoController - sensor_arduino measurement  
    "sensor_arduino": {
        "light_status": "integer", "light_lux": "float", "anemometer_1_mps": "float",
        "anemometer_2_mps": "float", "anemometer_3_mps": "float", "anemometer_4_mps": "float",
        "pressure_1_pascal": "float", "pressure_2_pascal": "float", "pressure_3_pascal": "float",
        "pressure_4_pascal": "float", "temperature_1_celsius": "float", "temperature_2_celsius": "float",
        "temperature_3_celsius": "float", "temperature_4_celsius": "float", "humidity_1_percent": "float",
        "humidity_2_percent": "float", "humidity_3_percent": "float", "humidity_4_percent": "float"
    },
    
    # Untuk SensorController - sensor_rs485 measurement
    "sensor_rs485": {
        "pt-100-temperature-1": "float", "pt-100-temperature-2": "float", "pt-100-temperature-3": "float",
        "pt-100-temperature-4": "float", "pt-100-temperature-5": "float", "pt-100-temperature-6": "float",
        "pt-100-temperature-7": "float", "pt-100-temperature-8": "float", "pt-100-temperature-9": "float",
        "pt-100-temperature-10": "float", "pt-100-temperature-11": "float", "pt-100-temperature-12": "float",
        "pt-100-temperature-13": "float", "pt-100-temperature-14": "float", "thm-30md-humidity-1": "float",
        "thm-30md-humidity-2": "float", "thm-30md-humidity-3": "float", "thm-30md-humidity-4": "float"
    },
    
    # Untuk RelayController - relay_data measurement
    "relay_data": {
        "relay_0": "integer", "relay_1": "integer", "relay_2": "integer", "relay_3": "integer",
        "relay_4": "integer", "relay_5": "integer", "relay_6": "integer", "relay_7": "integer"
    },
    
    # Data solar lama (untuk kompatibilitas)
    "solar_realtime": {
        "BAamps": "float", "BAperc": "float", "BAtemp": "float", "BAvolt": "float",
        "BAwatt": "float", "CTtemp": "float", "DCamps": "float", "DCVolt": "float",
        "DCwatt": "float", "PVwatt": "float", "PVamps": "float", "PVvolt": "float",
        "power_components_temp": "float"
    },
    "solar_statistics": {
        "consumed_energy_month": "float", "consumed_energy_today": "float",
        "consumed_energy_year": "float", "generated_energy_month": "float",
        "generated_energy_today": "float", "generated_energy_year": "float",
        "max_battery_voltage_today": "float", "max_pv_voltage_today": "float",
        "min_battery_voltage_today": "float", "min_pv_voltage_today": "float",
        "total_consumed_energy": "float", "total_generated_energy": "float"
    }
}

def generate_random_value(data_type):
    """Menghasilkan nilai acak berdasarkan tipe data."""
    if data_type == "float":
        return round(random.uniform(0.0, 50.0), 2)
    elif data_type == "integer":
        return random.randint(0, 1)
    return None

def create_influxdb_points():
    """Mengubah data dummy menjadi format InfluxDB Points (JSON structure)."""
    
    timestamp = datetime.utcnow().isoformat() + 'Z' 
    influx_points = []
    
    for measurement_name, fields_schema in MEASUREMENTS_SCHEMA.items():
        fields_data = {}
        
        # 1. Menghasilkan Field Data (nilai yang diukur)
        for field_name, data_type in fields_schema.items():
            value = generate_random_value(data_type)
            fields_data[field_name] = value

        # 2. Merakit InfluxDB Point
        point = {
            "measurement": measurement_name,
            "time": timestamp,
            "tags": {
                "device_id": DEVICE_TAG,
                "mac_address": MAC_ADDRESS  # Tag yang digunakan oleh semua controller
            },
            "fields": fields_data
        }
        influx_points.append(point)
        
    return influx_points

def main():
    """Menginisialisasi klien InfluxDB dan memulai loop penulisan data."""
    
    # 1. Inisialisasi Klien InfluxDB
    client = InfluxDBClient(
        host=INFLUXDB_HOST, 
        port=INFLUXDB_PORT, 
        username=INFLUXDB_USER, 
        password=INFLUXDB_PASSWORD
    )
    
    try:
        # 2. Buat database jika belum ada dan alihkan
        print(f"Memeriksa/membuat database '{INFLUXDB_DATABASE}'...")
        client.create_database(INFLUXDB_DATABASE)
        client.switch_database(INFLUXDB_DATABASE)

        print("\n--- InfluxDB 1.x Writer Dimulai ---")
        print(f"Host: {INFLUXDB_HOST}:{INFLUXDB_PORT}, Database: {INFLUXDB_DATABASE}")
        print(f"Device ID (Tag): {DEVICE_TAG}\n")

        while True:
            # 3. Buat Poin Data
            data_points = create_influxdb_points()
            
            # 4. Tulis Data ke InfluxDB
            client.write_points(data_points)
            
            print(f"[{datetime.now().strftime('%H:%M:%S')}] Berhasil menulis {len(data_points)} poin data.")

            # 5. Tunggu selama 2 detik
            time.sleep(2)
            
    except KeyboardInterrupt:
        print("\nPenulis data dihentikan oleh pengguna.")
    except Exception as e:
        print(f"\n[ERROR] Terjadi kesalahan koneksi/penulisan InfluxDB. Pastikan InfluxDB berjalan di {INFLUXDB_HOST}:{INFLUXDB_PORT} dan kredensial sudah benar.")
        print(f"Detail Error: {e}")
        
if __name__ == "__main__":
    main()