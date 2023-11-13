
export function generateRandomString(length: number = 8): string {
  const characters: string = 'abcdefghijklmnopqrstuvwxyz0123456789';
  let randomString: string = '';
  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    randomString += characters.charAt(randomIndex);
  }
  return randomString;
}