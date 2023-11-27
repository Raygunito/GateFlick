DEFAULT_PORT = 1234
DEFAULT_HOST = "localhost"
MAX_MESSAGE = int(500)
RECV_BUFFER_SIZE = int(1024)
STEP_PATTERNS = [
    r'^Com\d{9}$',
    r'^Portique\d{4}$',
    r'START UPDATE',
    r'RECEIVED'
]

WARNING_NO_RELATION_T_P = "WARN Ticket and Portique are not related"
WARNING_TICKET_USED = "WARN Ticket already used"
STEP_RESPONSES = [
    "VALID Ticket found",
    "VALID Serial found",
    "VALID Database updated",
    "VALID Closing socket..."
]

STEP_FAILURES = [
    "REJECT Ticket not found",
    "REJECT Serial not found",
    "REJECT Database not updated",
    "REJECT Closing socket..."
]