import { useParams } from "react-router-dom";

export default function ReviewTokenPage() {
    const { token } = useParams();

    return <h1>Review Token: {token}</h1>;
}
