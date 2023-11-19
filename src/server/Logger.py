import logging


def startLogger(className = __name__,log_file='serverLog.log', log_level=logging.DEBUG):
    logger = logging.getLogger(className)
    logger.setLevel(log_level)
    file_handler = logging.FileHandler(log_file)
    file_handler.setLevel(log_level)
    formatter = logging.Formatter('{name} - {levelname} - {asctime} - {message}', style='{', datefmt='%d/%m/%Y %H:%M:%S')
    file_handler.setFormatter(formatter)
    logger.addHandler(file_handler)
    return logger
