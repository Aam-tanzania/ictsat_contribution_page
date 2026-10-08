ALTER TABLE contributions
    ADD COLUMN initiation_started_at DATETIME(6) NULL,
    ADD COLUMN initiation_response_at DATETIME(6) NULL,
    ADD COLUMN initiation_response_ms INT UNSIGNED NULL,
    ADD COLUMN callback_received_at DATETIME(6) NULL,
    ADD COLUMN callback_latency_ms BIGINT UNSIGNED NULL;

CREATE TABLE payment_callback_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) NULL,
    callback_status VARCHAR(50) NULL,
    payload LONGTEXT NOT NULL,
    received_at DATETIME(6) NOT NULL,
    contribution_id INT NULL,
    initiation_to_callback_ms BIGINT UNSIGNED NULL,
    INDEX idx_callback_logs_order_id (order_id),
    INDEX idx_callback_logs_received_at (received_at)
);
